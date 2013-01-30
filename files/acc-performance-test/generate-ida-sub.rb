file_sub = File.open("ida.sub", "w")

file_sub.puts("universe                = vanilla")
file_sub.puts("requirements            = OpSys == \"LINUX\"")
file_sub.puts("should_transfer_files   = YES")
file_sub.puts("when_to_transfer_output = ON_EXIT")
file_sub.puts
file_sub.puts("executable              = ida.sh")
file_sub.puts("error                   = ida.err")
file_sub.puts("log                     = ida.log")
file_sub.puts("output                  = ida.out")
file_sub.puts

Dir.glob("*.arg") do |file_arg|
  # puts file_arg
  
  # open the file
  File.open(file_arg, "r") do |f|
    # read the first line
    args_line = f.readline
    # puts argc_line
    
    # parse the line for argumrnts
    args = args_line.split(" ")
    # puts args
    
    koncniCas = args[0]
    pga = args[1]
    acc_filename = args[2]
    
    for per in [0.1, 0.2, 0.3, 0.4, 0.5, 0.75, 1.0, 1.25, 1.5, 1.75, 2.0,]
      for xDamp in [0.01, 0.03, 0.05]
        
        out_filename = acc_filename + "_" + per.to_s + "_" + xDamp.to_s
        file_sub.puts("arguments               = " + koncniCas + " " + pga + " " + acc_filename + " " + per.to_s + " " + xDamp.to_s + " " + out_filename)
        file_sub.puts("transfer_input_files    = OpenSees_1_6_0_IKPIR, ida_template.tcl, SDOF_Spectra.tcl, " + acc_filename + ".acc, " + acc_filename + ".AEi")
        file_sub.puts("queue")
        file_sub.puts
      end
    end
  end
end
