import { infoCrypto } from "../../object/myobject";
import { Bar } from 'react-chartjs-2';
import 'chart.js/auto'; // Include all required chart types
const Graphic = () => {
  const months = {
    "01": "Jan",
    "02": "Fev",
    "03": "Mar",
    "04": "Abr",
    "05": "Mai",
    "06": "Jun",
    "07": "Jul",
    "08": "Ago",
    "09": "Set",
    "10": "Out",
    "11": "Nov",
    "12": "Dez"
};
 
    const colorPalette = ['#2980b9', '#f1c40f', '#d35400', '#2ecc71', '#e74c3c', '#b04c3c', '#a1c40f', '#b35700', '#5ecc01', '#a72c3c', '#b04c51']
  

  const salesByDate =()=>{
    const date = new Date(date).toISOString().split('T')[0]
    const currentDate = new Date(date)
    const day = currentDate.getDate()
    const month = months[String(currentDate.getMonth() + 1).padStart(2, '0')]
    const year = currentDate.getFullYear()
    const fortDate = `${day} ${month} ${year}`
  }

  
  
  const generateColors = (count)=>{
    return Array.from({ length: count }, (_, index)=> colorPalette[index % colorPalette.length])
  }
  const chartData = {
    labels: "high prices",
    datasets:[
      {
        label:"high price",
        data: [130, 400, 100, 500],
        backgroundColor: generateColors(10),
        borderColor: generateColors(10),
        borderWidth: 1,

      },
    ],
  }

  const chartOptions = {
    plugins:{
      legend:{
        display: false
      },
    },
  }
  return (
    <div style={{height:"100%", minWidth:"calc(100vh - 55px)", overflowY:"auto", overflowX: "auto"}}>
      <div>
        <h1>BTC</h1>
      </div>
      <Bar data={chartData} className="" options={chartOptions}/>
    </div>
  )
}

export default Graphic